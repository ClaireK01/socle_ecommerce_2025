<?php

namespace App\Events;

class Events{

    //socket
    const USER_CONNECETD = "user.connected";
    const USER_DISCONNECTED = "user.disconnected";
    const USER_MESSAGE = "user.message";
    const CONVERSATION_MESSAGE_SENT = "conversation.message.sent";
    const CONVERSATION_OPENED = "conversation.opened";

    //mail
    const MAIL_ORDER = "mail.order";
    const MAIL_MAJ_CLIENT_ORDER = "mail.maj.clientorder";


    //order
    const ORDER_STATE_CHANGED = "order.state.changed";


}