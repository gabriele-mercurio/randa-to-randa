<?php

namespace App\Util;

class Constants
{
    // Chapter states
    public const CHAPTER_STATE_CHAPTER = 'CHAPTER';
    public const CHAPTER_STATE_CLOSED = 'CLOSED';
    public const CHAPTER_STATE_CORE_GROUP = 'CORE_GROUP';
    public const CHAPTER_STATE_PROJECT = 'PROJECT';
    public const CHAPTER_STATE_SUSPENDED = 'SUSPENDED';

    // Cost Types
    public const COST_TYPE_BASEDIR = 'BaseDir';
    public const COST_TYPE_NEW = 'Nuovo';
    public const COST_TYPE_RENEWAL = 'Rinnovo';
    public const COST_TYPE_ROYALTY = 'Royalty';
    public const COST_TYPE_RS = 'RS';

    // PayTypes
    public const PAY_TYPE_ANNUAL = 'ANNUAL';
    public const PAY_TYPE_MONTHLY = 'MONTHLY';

    // Rana LifeCycle statuses
    public const RANA_LIFECYCLE_STATUS_APPROVED = 'APPROVED';
    public const RANA_LIFECYCLE_STATUS_PROPOSED = 'PROPOSED';
    public const RANA_LIFECYCLE_STATUS_REFUSED = 'REFUSED';
    public const RANA_LIFECYCLE_STATUS_TODO = 'TODO';

    // Roles
    public const ROLE_AREA = 'AREA';
    public const ROLE_ASSISTANT = 'ASSISTANT';
    public const ROLE_EXECUTIVE = 'EXECUTIVE';
    public const ROLE_NATIONAL = 'NATIONAL';

    // TimeSlots
    public const TIMESLOT_T0 = 'T0';
    public const TIMESLOT_T1 = 'T1';
    public const TIMESLOT_T2 = 'T2';
    public const TIMESLOT_T3 = 'T3';
    public const TIMESLOT_T4 = 'T4';

    // ValueTypes
    public const VALUE_TYPE_APPROVED = 'APPR';
    public const VALUE_TYPE_CONSUMPTIVE = 'CONS';
    public const VALUE_TYPE_PROPOSED = 'PROP';
}
