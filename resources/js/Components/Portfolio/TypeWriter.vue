<template>
    <span class="inline-block">
        {{ displayText }}<span 
            v-if="showCursor"
            class="inline-block w-0.5 h-6 bg-primary ml-1 align-middle cursor-blink"
        ></span>
    </span>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    words: {
        type: Array,
        required: true,
    },
    delay: {
        type: Number,
        default: 2000,
    },
    typingSpeed: {
        type: Number,
        default: 100,
    },
    deletingSpeed: {
        type: Number,
        default: 50,
    },
});

const displayText = ref('');
const currentWordIndex = ref(0);
const currentCharIndex = ref(0);
const showCursor = ref(true);
const isTyping = ref(false);
const isDeleting = ref(false);
let intervalId = null;
let timeoutId = null;

const startTyping = () => {
    if (isTyping.value || isDeleting.value) return;
    
    isTyping.value = true;
    showCursor.value = true;
    
    intervalId = setInterval(() => {
        const currentWord = props.words[currentWordIndex.value];
        
        if (!currentWord) {
            clearInterval(intervalId);
            isTyping.value = false;
            return;
        }
        
        if (currentCharIndex.value < currentWord.length) {
            displayText.value += currentWord[currentCharIndex.value];
            currentCharIndex.value++;
        } else {
            // Palabra completada
            clearInterval(intervalId);
            isTyping.value = false;
            showCursor.value = true;
            
            // Esperar antes de borrar
            timeoutId = setTimeout(() => {
                startDeleting();
            }, props.delay);
        }
    }, props.typingSpeed);
};

const startDeleting = () => {
    isDeleting.value = true;
    showCursor.value = true;
    
    intervalId = setInterval(() => {
        if (displayText.value.length > 0) {
            displayText.value = displayText.value.slice(0, -1);
        } else {
            // Palabra borrada completamente
            clearInterval(intervalId);
            isDeleting.value = false;
            
            // Pasar a la siguiente palabra
            if (currentWordIndex.value === props.words.length - 1) {
                currentWordIndex.value = 0;
            } else {
                currentWordIndex.value++;
            }
            currentCharIndex.value = 0;
            
            // Pequeña pausa antes de empezar a escribir la siguiente palabra
            timeoutId = setTimeout(() => {
                startTyping();
            }, 500);
        }
    }, props.deletingSpeed);
};

const reset = () => {
    if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
    }
    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
    displayText.value = '';
    currentWordIndex.value = 0;
    currentCharIndex.value = 0;
    isTyping.value = false;
    isDeleting.value = false;
    showCursor.value = true;
};

onMounted(() => {
    if (props.words && props.words.length > 0) {
        // Pequeño delay inicial antes de empezar
        timeoutId = setTimeout(() => {
            startTyping();
        }, 500);
    }
});

onUnmounted(() => {
    reset();
});

// Reiniciar cuando cambien las palabras
watch(() => props.words, () => {
    reset();
    if (props.words && props.words.length > 0) {
        timeoutId = setTimeout(() => {
            startTyping();
        }, 500);
    }
}, { immediate: false });
</script>

<style scoped>
.cursor-blink {
    animation: blink 1s step-end infinite;
}

@keyframes blink {
    0%, 50% {
        opacity: 1;
    }
    51%, 100% {
        opacity: 0;
    }
}
</style>



