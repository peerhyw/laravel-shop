<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Auth\Events\Registered;

class RegisteredListener implements ShouldQueue
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
     * @param  object  $event
     * @return void
     */
    //当事件被触发时，对应事件的监听器的handle()方法就会被调用
    public function handle(Registered $event)
    {
        //获取到刚刚注册的用户
        $user = $event->user;
        //调用notify发送通知
        $user->notify(new EmailVerificationNotification());
    }
}
