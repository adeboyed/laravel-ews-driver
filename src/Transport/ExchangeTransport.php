<?php

namespace Adeboyed\LaravelExchangeDriver\Transport;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\CreateItemType;

use \jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;

use \jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;

use \jamesiarmes\PhpEws\Type\BodyType;
use \jamesiarmes\PhpEws\Type\EmailAddressType;
use \jamesiarmes\PhpEws\Type\MessageType;
use \jamesiarmes\PhpEws\Type\SingleRecipientType;


class ExchangeTransport extends Transport
{

    protected $host;
    protected $username;
    protected $password;
    protected $messageDispositionType;

    public function __construct($host, $username, $password, $messageDispositionType)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->messageDispositionType = $messageDispositionType;
    }

    public function send(Swift_Mime_SimpleMessage $simpleMessage, &$failedRecipients = null)
    {

        $this->beforeSendPerformed($simpleMessage);

        $client = new Client(
            $this->host,
            $this->username,
            $this->password
        );

        $request = new CreateItemType();
        $request->Items = new NonEmptyArrayOfAllItemsType();

        $request->MessageDisposition = $this->messageDispositionType;

        // Create the ewsMessage.
        $ewsMessage = new MessageType();
        $ewsMessage->Subject = $simpleMessage->getSubject();
        $ewsMessage->ToRecipients = new ArrayOfRecipientsType();

        // Set the sender.
        $ewsMessage->From = new SingleRecipientType();
        $ewsMessage->From->Mailbox = new EmailAddressType();
        $ewsMessage->From->Mailbox->EmailAddress = config('mail.from.address');

        // Set the recipient.
        foreach ($this->allContacts($simpleMessage) as $email => $name) {
            $recipient = new EmailAddressType();
            $recipient->EmailAddress = $email;
            if ($name != null) {
                $recipient->Name = $name;
            }
            $ewsMessage->ToRecipients->Mailbox[] = $recipient;
        }

        // Set the ewsMessage body.
        $ewsMessage->Body = new BodyType();
        $ewsMessage->Body->BodyType = BodyTypeType::HTML;
        $ewsMessage->Body->_ = $simpleMessage->getBody();

        $request->Items->Message[] = $ewsMessage;
        $response = $client->CreateItem($request);

        // Iterate over the results, printing any error messages or ewsMessage ids.
        $response_messages = $response->ResponseMessages->CreateItemResponseMessage;
        foreach ($response_messages as $response_message) {
            // Make sure the request succeeded.
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $ewsMessage
                    = $response_message->MessageText;
                fwrite(STDERR, "Message failed to create with \"$code: $ewsMessage\"\n");
                continue;
            }
        }


        $this->sendPerformed($simpleMessage);

        return $this->numberOfRecipients($simpleMessage);
    }

    /**
     * Get all of the contacts for the ewsMessage
     *
     * @param \Swift_Mime_SimpleMessage $ewsMessage
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array) $message->getTo(),
            (array) $message->getCc(),
            (array) $message->getBcc()
        );
    }
}
