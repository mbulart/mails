@php
    $previewHtml = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', (string) $html) ?? (string) $html;
@endphp

<div class="space-y-4">
    <div>
        <p class="text-sm text-gray-500">Sujet</p>
        <p class="font-medium">{{ $subject }}</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white">
        <iframe
            class="h-[70vh] w-full rounded-lg"
            sandbox="allow-same-origin"
            srcdoc="{{ e($previewHtml) }}"
            title="Prévisualisation HTML du template"
        ></iframe>
    </div>
</div>
