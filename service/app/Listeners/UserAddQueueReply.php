<?php

namespace App\Listeners;

use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Interop\Amqp\AmqpTopic;

class UserAddQueueReply
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  string  $event
     * @param  array  $payload
     *
     * @return bool
     */
    public function handle($event, $payload)
    {
        info('Message recieved');
        $validator = Validator::make($payload, [
            'action'   => [
                'string',
                'required',
                'max:10',
                Rule::in(['add_user'])
            ],
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100',
            'location' => 'required|string|max:100'
        ]);

        // Handle validation error
        if ($validator->fails()) {
            return $this->sendReply($payload['reply_to']['exchange'], $payload['reply_to']['queue'], [
                'id'         => null,
                'error_code' => 1,
                'error_msg'  => (string) $validator->errors()
            ]);
        }
        $user           = new User();
        $user->name     = $payload['name'];
        $user->email    = $payload['email'];
        $user->location = $payload['location'];
        $user->save();

        return $this->sendReply($payload['reply_to']['exchange'], $payload['reply_to']['queue'], [
            'id'         => $user->id,
            'error_code' => 0,
            'error_msg'  => ''
        ]);
    }

    /**
     * @param  string  $exchange  Name of exchange to send
     * @param  string  $queue  Name of queue to send
     * @param  array  $payload  Data to send
     *
     * @return bool
     */
    private function sendReply(string $exchange, string $queue, array $payload)
    {
        // Change topic to reply
        Config::set('queue.connections.rabbitmq.exchange', $exchange);
        app()->instance(AmqpTopic::class, null);

        return publish($queue, $payload);
    }

}
