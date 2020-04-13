<?php


namespace App\Model;


use Illuminate\Support\Collection;

class MetadataRequest extends ArchiveRequest
{
    // Strip out any supplied deadbands because they don't apply
    // to a request to change only metadata.
    public function channels(){
        return $this->channelCollection->map(function ($item, $key) {
            return ['channel' => $item['channel']];
        });
    }


}
