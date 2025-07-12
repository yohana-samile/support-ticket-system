<?php

namespace App\Models\Ticket;


use App\Models\Access\Client;
use App\Models\Access\User;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\SubTopic;
use App\Models\TertiaryTopic;
use App\Models\Topic;

trait TicketRelationship
{
    public function client()
    {
        return $this->belongsTo(Client::class);
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
        return $this->belongsTo(SubTopic::class);
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
