@props(['headers' => []])

<div class="table-container">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-border dark:divide-border-dark']) }}>
            @if(count($headers) > 0)
                <thead class="table-header">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="table-th">{{ $header }}</th>
                        @endforeach
                        @if($attributes->has('actions'))
                            <th scope="col" class="table-th text-right">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-border dark:divide-border-dark bg-card dark:bg-card-dark">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
