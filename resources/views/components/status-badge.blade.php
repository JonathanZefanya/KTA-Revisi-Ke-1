@php($type = ($type ?? 'neutral'))
@php($map = [
 'success' => 'success',
 'warning' => 'warning',
 'danger'  => 'danger',
 'error'   => 'danger',
 'info'    => 'info',
 'neutral' => 'neutral'
])
@php($cls = $map[$type] ?? 'neutral')
<span {{ $attributes->merge(['class'=>'status-badge '.$cls]) }}>{{ $slot }}</span>