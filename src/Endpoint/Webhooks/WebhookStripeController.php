<?php

namespace Entersis\Endpoint\Webhooks;

use Entersis\Endpoint\EndpointBase;
use Entersis\Enum\General;

class WebhookStripeController extends EndpointBase
{

    public function post()
    {
        // Receba o corpo da solicitação POST
        $webhookData = json_decode(file_get_contents("php://input"), true);

        if (!$webhookData) {
            $this->respond(400, 'Invalid JSON data');
            return;
        }

        $stripe = new \Stripe\StripeClient(General::SECRET_KEY_STRIPE);
        $endpoint_secret = '';

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            echo '<pre>';
            var_dump($event);
            echo '</pre>';
        } catch(\UnexpectedValueException $e) {
            $this->respondError('Bad Request');
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            $this->respondError('Bad Request');
            exit();
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        // Implemente a lógica de processamento do webhook com base nos dados recebidos
        // ...

        // Verifique a autenticidade do webhook, se necessário
        if (!$this->isWebhookValid($webhookData)) {
            $this->respondError('Unauthorized', 401);
            return;
        }

        // Responda ao webhook para confirmar o recebimento
        $this->respondSuccess('Webhook received successfully');
    }

    private function isWebhookValid($data)
    {
        // Implemente a validação da autenticidade do webhook aqui
        // Verifique a assinatura ou outros detalhes de autenticação
        // Retorne true se o webhook for válido, false caso contrário
        return true; // Exemplo simples de autenticidade
    }

}