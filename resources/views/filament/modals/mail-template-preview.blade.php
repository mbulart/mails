@php
    $previewHtml = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', (string) $html) ?? (string) $html;
    $previewHtml = preg_replace(
        '/<\/head>/i',
        '<style>.wrapper{padding:0 !important;}.card{max-width:100% !important;margin:0 !important;border-radius:0 !important;box-shadow:none !important;}</style></head>',
        $previewHtml,
        1,
    ) ?? $previewHtml;
    $previewSrc = 'data:text/html;charset=utf-8;base64,'.base64_encode($previewHtml);
@endphp

<div class="space-y-4">
    <div>
        <p class="text-sm text-gray-500">Sujet</p>
        <p class="font-medium">{{ $subject }}</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white">
        <iframe
            class="h-[70vh] w-full rounded-lg"
            sandbox="allow-same-origin allow-scripts"
            src="{{ $previewSrc }}"
            title="Prévisualisation HTML du template"
        ></iframe>
    </div>
</div>
