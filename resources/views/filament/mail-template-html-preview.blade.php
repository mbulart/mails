@props(['html' => ''])

<iframe
    class="fi-modal-content w-full rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-950"
    style="height: 75vh"
    sandbox=""
    title="Apercu HTML"
    srcdoc="{{ htmlspecialchars($html, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8') }}"
></iframe>
<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
    Apercu avec des valeurs d'exemple. Le sandbox du navigateur desactive les scripts.
</p>
