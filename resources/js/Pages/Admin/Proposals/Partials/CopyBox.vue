<template>
    <div class="rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex justify-between items-center bg-gray-50 px-4 py-2 border-b border-gray-200">
            <span class="text-sm font-semibold text-gray-700">{{ label }}</span>
            <button
                type="button"
                @click="copy"
                class="inline-flex items-center gap-1 text-sm font-medium px-3 py-1 rounded-lg transition-colors"
                :class="copied ? 'bg-green-100 text-green-700' : 'bg-primary/10 text-primary hover:bg-primary/20'"
            >
                <svg v-if="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                {{ copied ? 'Copiado' : 'Copiar' }}
            </button>
        </div>
        <pre class="px-4 py-3 text-sm text-gray-800 whitespace-pre-wrap break-words font-sans" :class="mono ? 'font-mono text-base' : ''">{{ value }}</pre>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    label: String,
    value: { type: [String, Number], default: '' },
    mono: { type: Boolean, default: false },
});

const copied = ref(false);
async function copy() {
    try {
        await navigator.clipboard.writeText(String(props.value ?? ''));
    } catch (e) {
        // fallback
        const ta = document.createElement('textarea');
        ta.value = String(props.value ?? '');
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    }
    copied.value = true;
    setTimeout(() => (copied.value = false), 1500);
}
</script>
