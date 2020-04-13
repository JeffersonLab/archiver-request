
Requester: {{$archiveRequest->email()}}
Deployment: {{$archiveRequest->deployment}}
Group: {{$archiveRequest->group}}

@if ($archiveRequest->comments)
Comments
--------

{{$archiveRequest->comments}}

@endif


Channel Specifications:
-----------------------

@foreach($archiveRequest->channels() as $item)
{{$item['channel']}}   {{isset($item['deadband']) ? $item['deadband'] : ''}}
@endforeach


