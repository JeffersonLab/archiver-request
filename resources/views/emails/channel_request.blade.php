
Request: {{$archiveRequest->requestedTypeLabel()}}
Requester: {{$archiveRequest->email()}}
Deployment: {{$archiveRequest->deployment}}
Group: {{$archiveRequest->group}}
@if ($archiveRequest->duration)
Duration: {{$archiveRequest->duration}}
@endif
@if ($archiveRequest->keep)
Keep: {{$archiveRequest->keep}}
@endif

@if ($archiveRequest->comments)
Comments
--------

{!! $archiveRequest->comments !!}

@endif


Channel Specifications:
-----------------------

@foreach($archiveRequest->channels() as $item)
{{$item['channel']}}   {{isset($item['deadband']) ? $item['deadband'] : ''}}
@endforeach


