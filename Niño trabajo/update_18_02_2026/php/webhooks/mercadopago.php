<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mercadopago.php';

// Log para debugging
$log_file = __DIR__ . '/../../logs/mercadopago_webhooks.log';
if (!is_dir(dirname($log_file))) {
    mkdir(dirname($log_file), 0755, true);
}

$log_message = date('Y-m-d H:i:s') . " - POST DATA: " . json_encode($_POST) . "\n";
file_put_contents($log_file, $log_message, FILE_APPEND);

// Mercado Pago solo envía GET con topic y id
$topic = isset($_GET['topic']) ? $_GET['topic'] : (isset($_POST['topic']) ? $_POST['topic'] : '');
$resource_id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : '');

if (!$topic || !$resource_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'topic e id requeridos']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Si es pago
    if ($topic === 'payment') {
        // Obtener detalles del pago desde Mercado Pago
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => MERCADOPAGO_API_URL . "/v1/payments/" . $resource_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . MERCADOPAGO_ACCESS_TOKEN
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error cURL: " . $err . "\n", FILE_APPEND);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos del pago']);
            exit;
        }

        $payment_data = json_decode($response, true);

        if (!isset($payment_data['id'])) {
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Respuesta inválida: " . $response . "\n", FILE_APPEND);
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Datos de pago inválidos']);
            exit;
        }

        $payment_status = $payment_data['status'];
        $external_reference = $payment_data['external_reference'] ?? '';

        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Payment status: " . $payment_status . " - External ref: " . $external_reference . "\n", FILE_APPEND);

        // Buscar la reserva por id_mercadopago
        $stmt = $conn->prepare('SELECT id, id_mercadopago FROM reservas WHERE id_mercadopago = :id_mercadopago LIMIT 1');
        $stmt->bindParam(':id_mercadopago', $resource_id);
        $stmt->execute();
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Reserva no encontrada para: " . $resource_id . "\n", FILE_APPEND);
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Reserva no encontrada']);
            exit;
        }

        $id_reserva = $reserva['id'];

        // Actualizar estado del pago
        $estado_pago = '';
        $estado_reserva = '';

        switch ($payment_status) {
            case 'approved':
                $estado_pago = 'completado';
                $estado_reserva = 'confirmada';
                break;
            case 'pending':
                $estado_pago = 'pendiente';
                $estado_reserva = 'pendiente';
                break;
            case 'rejected':
            case 'cancelled':
                $estado_pago = 'rechazado';
                $estado_reserva = 'cancelada';
                break;
            case 'in_process':
                $estado_pago = 'procesando';
                $estado_reserva = 'pendiente';
                break;
            default:
                $estado_pago = $payment_status;
                $estado_reserva = 'pendiente';
        }

        // Actualizar tabla de pagos
        $stmtUpdatePago = $conn->prepare('
            UPDATE pagos 
            SET estado = :estado, 
                json_respuesta = :json_respuesta,
                fecha_actualizacion = NOW()
            WHERE id_mercadopago = :id_mercadopago
        ');
        $stmtUpdatePago->bindParam(':estado', $estado_pago);
        $stmtUpdatePago->bindParam(':json_respuesta', $response);
        $stmtUpdatePago->bindParam(':id_mercadopago', $resource_id);
        $stmtUpdatePago->execute();

        // Actualizar tabla de reservas
        $stmtUpdateReserva = $conn->prepare('
            UPDATE reservas 
            SET estado = :estado,
                pago_confirmado = :pago_confirmado,
                fecha_pago = NOW()
            WHERE id = :id_reserva
        ');
        $pago_confirmado = ($payment_status === 'approved') ? 1 : 0;
        $stmtUpdateReserva->bindParam(':estado', $estado_reserva);
        $stmtUpdateReserva->bindParam(':pago_confirmado', $pago_confirmado, PDO::PARAM_INT);
        $stmtUpdateReserva->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
        $stmtUpdateReserva->execute();

        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Reserva actualizada: ID=" . $id_reserva . ", Estado=" . $estado_reserva . "\n", FILE_APPEND);
    }

    // Si es preferencia
    elseif ($topic === 'chargebacks') {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Chargeback received: " . $resource_id . "\n", FILE_APPEND);
    }

    echo json_encode(['status' => 'success']);
    exit;

} catch (Exception $ex) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Exception: " . $ex->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $ex->getMessage()]);
    exit;
}

?>
