<script>
export default {
  props: ["label", "modelValue"],

  emits: ["update:modelValue"],
};
</script>

<template>
  <div>
    <div>
      <label v-if="label" v-text="label"></label>
    </div>
    <div
      class="input-switch"
      :class="modelValue && 'checked'"
      @click="$emit('update:modelValue', !modelValue)"
    ></div>
  </div>
</template>

<style scoped>
.input-switch,
.input-switch:after {
  box-sizing: border-box;
  color: #6c868e;
  background: #fff;
  border: 2px solid #bbcdd2;
  border-radius: 20px;
  -webkit-transition: all 0.5s;
  transition: all 0.5s;
  vertical-align: middle;
}
.input-switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 20px;
  cursor: pointer;
  margin: -2px 4px 0 0;
  background: #fffbd3;
}
.input-switch:after {
  font-family: "Material Icons", Arial, Verdana, Tahoma, sans-serif;
  font-style: normal;
  font-weight: 400;
  text-transform: none;
  letter-spacing: normal;
  word-wrap: normal;
  white-space: nowrap;
  direction: ltr;
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-feature-settings: "liga";
  font-feature-settings: "liga";
  content: "close";
  position: absolute;
  top: -4px;
  left: -4px;
  display: block;
  width: 24px;
  height: 24px;
  font-size: 16px;
  line-height: 20px;
  text-align: center;
  transform: rotate(-180deg);
}
.input-switch.checked {
  background-color: #25b9d7;
}
.input-switch.checked::after {
  left: 16px;
  color: #25b9d7;
  content: "check";
  transform: rotate(0deg);
}
</style>
