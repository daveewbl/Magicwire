<script>
import List from "@/views/List.vue";
import Edit from "@/views/Edit.vue";
import { computed } from "vue";

function getActiveLanguage() {
  return window.languages.find((language) => language.isContextLang);
}

const items = window.items;

export default {
  components: { List, Edit },
  data() {
    return {
      view: "List",
      items: items,
      currentlyEditing: null,
    };
  },

  methods: {
    changeView(view) {
      this.view = view;
    },
  },

  provide() {
    return {
      items: computed(() => this.items),
      setItems: (items) => {
        this.items = items;
      },

      activeLanguageId: getActiveLanguage().id,

      view: computed(() => this.view),
      setCurrentView: (view) => {
        this.view = view;
      },

      currentlyEditing: computed(() => {
        return this.currentlyEditing;
      }),
      setCurrentlyEditing: (item) => {
        this.currentlyEditing = item;
      },
    };
  },
};
</script>

<template>
  <div>
    <component :is="view" @changeView="changeView"></component>
  </div>
</template>
