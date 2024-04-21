<?php

use Bytexr\QueueableBulkActions\Enums\StatusEnum;

return [
    StatusEnum::QUEUED->value => 'Queued',
    StatusEnum::IN_PROGRESS->value => 'In progress',
    StatusEnum::FINISHED->value => 'Finished',
    StatusEnum::FAILED->value => 'Failed',
];
