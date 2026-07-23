@props(['title', 'description' => null, 'action' => null, 'actionUrl' => null])

<div class="empty-state">
    <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-5">
        <i class="fa-regular fa-inbox text-3xl text-slate-400"></i>
    </div>
    <h3 class="text-lg font-semibold text-text-primary dark:text-text-dark">{{ $title }}</h3>
    @if($description)
        <p class="mt-1.5 text-sm text-text-secondary dark:text-slate-400 max-w-sm text-center">{{ $description }}</p>
    @endif
    @if($action && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn-primary mt-6">{{ $action }}</a>
    @endif
</div>
