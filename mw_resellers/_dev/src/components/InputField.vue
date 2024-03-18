<script>
export default {
  props: ["label", "required", "modelValue"],

  expose: ["getFieldElement", "setError"],

  emits: ["update:modelValue"],

  data() {
    return {
      type: this.type ?? "text",
      error: false,
    };
  },

  methods: {
    getFieldElement() {
      return this.$refs.fieldElement;
    },
    setError(error) {
      this.error = error;
    },
  },
};
</script>

<template>
  <div class="form-group">
    <label>{{ label }} <sup class="required" v-if="required">*</sup></label>
    <input
      class="form-control"
      :type="type"
      :value="modelValue"
      :required="required"
      ref="fieldElement"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <div class="error text-small" v-if="error && required">
      {{ label }} è un campo obbligatorio
    </div>
  </div>
</template>

<style scoped>
.required {
  color: red;
}

.error {
  color: red;
}
</style>
