<?php

namespace App\Models\Ticket;


use App\Models\Access\Client;
use App\Models\Access\User;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\PaymentChannel;
use App\Models\SaasApp;
use App\Models\SenderId;
use App\Models\SubTopic;
use App\Models\TertiaryTopic;
use App\Models\TicketStatusHistory;
use App\Models\Topic;

trait TicketRelationship
{
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paymentChannel()
    {
        return $this->belongsTo(PaymentChannel::class, 'payment_channel_id');
    }
    public function sender()
    {
        return $this->belongsTo(SenderId::class, 'sender_id');
    }

    public function saasApp()
    {
        return $this->belongsTo(SaasApp::class, 'saas_app_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(TicketStatusHistory::class)->with('changedByUser')->latest('changed_at');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function subtopic()
    {
        return $this->belongsTo(SubTopic::class, 'sub_topic_id');
    }

    public function tertiaryTopic()
    {
        return $this->belongsTo(TertiaryTopic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
