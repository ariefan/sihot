<template>
    <div class="relative w-full" @keydown.down.prevent="onArrowDown" @keydown.up.prevent="onArrowUp"
        @keydown.enter.prevent="onEnter">
        <input type="text" :value="displayLabel" @input="onInput($event.target.value)" :placeholder="placeholder" class="bg-gray-50
        border
        border-gray-300
        text-gray-900
        rounded-md
        focus:ring-primary-500
        focus:border-primary-500
        block
        w-full
        dark:bg-gray-900
        dark:border-gray-700
        dark:placeholder-gray-400
        dark:text-gray-100
        dark:focus:ring-primary-600
        dark:focus:border-primary-600" @focus="isDropdownVisible = true" @blur="hideDropdown" />

        <ul v-if="isDropdownVisible && suggestions.length"
            class="absolute w-full bg-white border border-gray-300 rounded-md mt-1 z-10">
            <li v-for="(suggestion, index) in suggestions" :key="index" @click="selectSuggestion(suggestion)"
                @mousedown="selectSuggestion(suggestion)"
                :class="['cursor-pointer px-4 py-2 hover:bg-gray-100', { 'bg-gray-200': index === highlightedIndex }]">
                {{ suggestion.label }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
import { debounce } from 'lodash';

// Props for configurable behavior
const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    apiUrl: {
        type: String,
        default: '/api/products/suggestions'  // Default URL
    },
    debounceTime: {
        type: Number,
        default: 500  // Default debounce time in milliseconds
    },
    placeholder: {
        type: String,
        default: 'Search...'  // Default input placeholder
    },
    label: {
        type: String,
        default: ''  // Default label to be shown in the input (if provided)
    }
});

const emit = defineEmits(['update:modelValue']);

// Define reactive data
const query = ref('');  // Query is for displaying input as the user types
const selectedValue = ref(props.modelValue);  // Holds the selected value
const suggestions = ref([]);
const isDropdownVisible = ref(false);
const highlightedIndex = ref(-1);  // Tracks the current highlighted suggestion index
const displayLabel = ref(props.label);  // This is the label shown in the input field, initialized with label

// Watch for changes in v-model (in case it's updated from parent)
watch(() => props.modelValue, (newValue) => {
    selectedValue.value = newValue;
    // When the value changes externally, reset the display label
    const selectedSuggestion = suggestions.value.find(suggestion => suggestion.value === newValue);
    displayLabel.value = selectedSuggestion ? selectedSuggestion.label : props.label;  // Use label if no suggestion found
});

// Function to search for suggestions (returns value-label pairs)
const searchSuggestions = async () => {
    if (query.value.length > 0) {
        const response = await fetch(`${props.apiUrl}?q=${query.value}`);
        const data = await response.json();
        suggestions.value = data;
        highlightedIndex.value = -1;  // Reset highlighted index when new suggestions load
    } else {
        suggestions.value = [];
    }
};

// Debounced search function using lodash debounce
const debouncedSearch = debounce(searchSuggestions, props.debounceTime);

// Function triggered on input event
const onInput = (value) => {
    query.value = value;
    displayLabel.value = value;  // Keep showing the input field value
    isDropdownVisible.value = true;
    debouncedSearch();
};

// Function to select suggestion
const selectSuggestion = (suggestion) => {
    displayLabel.value = suggestion.label;  // Keep the label in the input
    selectedValue.value = suggestion.value;  // Store the selected value (like a select input)
    emit('update:modelValue', selectedValue.value);  // Emit the selected value to parent
    isDropdownVisible.value = false;  // Close the dropdown after selection
};

// Function to handle arrow down key
const onArrowDown = () => {
    if (highlightedIndex.value < suggestions.value.length - 1) {
        highlightedIndex.value++;
    } else {
        highlightedIndex.value = 0;  // Loop back to the top
    }
};

// Function to handle arrow up key
const onArrowUp = () => {
    if (highlightedIndex.value > 0) {
        highlightedIndex.value--;
    } else {
        highlightedIndex.value = suggestions.value.length - 1;  // Loop to the bottom
    }
};

// Function to handle enter key
const onEnter = () => {
    if (highlightedIndex.value !== -1 && suggestions.value[highlightedIndex.value]) {
        selectSuggestion(suggestions.value[highlightedIndex.value]);
    }
};

// Function to hide dropdown on blur
const hideDropdown = () => {
    setTimeout(() => isDropdownVisible.value = false, 100);  // Small delay to allow for click
};
</script>

<style scoped>
/* Tailwind handles the styling, no additional styles are necessary */
</style>
