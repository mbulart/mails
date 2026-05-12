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

<div class="not-prose space-y-4" style="width: min(82vw, 1120px); max-width: calc(100vw - 4rem);">
    <div>
        <p class="text-sm text-gray-500">Sujet</p>
        <p class="font-medium">{{ $subject }}</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white" style="width: 100%; max-width: none;">
        <iframe
            class="rounded-lg"
            style="display: block; width: 100%; max-width: none; height: 70vh; border: 0;"
            sandbox="allow-same-origin allow-scripts"
            src="{{ $previewSrc }}"
            title="Prévisualisation HTML du template"
        ></iframe>
    </div>
</div>
