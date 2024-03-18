<script>
import axios from "axios";
import InputSwitch from "../components/InputSwitch.vue";

const urls = window.postUrls;

export default {
  components: { InputSwitch },

  data() {
    return {
      error: null,
    };
  },

  methods: {
    updateItemActiveState(id, state) {
      axios
        .post(urls.updateActiveState, { id, state })
        .catch((error) => console.log(error));
    },

    editItem(item) {
      this.setCurrentlyEditing(item);
      this.setCurrentView("Edit");
    },

    deleteItem(itemId) {
      axios
        .post(urls.delete, { id: itemId })
        .then(({ data }) => {
          this.setItems(data.items);
        })
        .catch(
          ({ response }) =>
            (this.error = `${response.status} - ${response.data}`)
        );
    },

    filterItems(query) {
      query = query.target.value;

      if (!query) {
        this.setItems(
          this.items.map((item) => {
            item.isVisible = true;
            return item;
          })
        );
      }

      this.setItems(
        this.items.map((item) => {
          item.isVisible = item.name
            .toLowerCase()
            .includes(query.toLowerCase());
          return item;
        })
      );
    },
  },

  inject: ["setCurrentlyEditing", "setCurrentView", "items", "setItems"],
};
</script>

<template>
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col">
          <input
            class="form-control col-3"
            placeholder="Cerca..."
            @input="filterItems"
          />
        </div>
        <div class="col-auto">
          <button class="btn btn-primary" @click="$emit('changeView', 'Edit')">
            <i class="material-icons">add_circle_outline</i>
            Nuovo rivenditore
          </button>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="row" v-if="error">
        <div class="col">
          <div class="alert alert-danger" v-text="error"></div>
        </div>
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Indirizzo</th>
            <th>Attivo</th>
            <th>Gruppo</th>
            <th class="action-column">Azioni</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, key) in items.filter((i) => i.isVisible)"
            :key="key"
          >
            <td v-text="item.id"></td>
            <td v-text="item.name"></td>
            <td v-text="item.address"></td>
            <td>
              <input-switch
                v-model="item.active"
                @update:model-value="updateItemActiveState(item.id, $event)"
              ></input-switch>
            </td>
            <td v-text="item.group ? item.group.name : null"></td>
            <td>
              <div class="btn-group ml-2">
                <a
                  class="btn btn-primary edit-btn"
                  href
                  @click.prevent="() => editItem(item)"
                >
                  <i class="material-icons">edit</i>
                  <span class="title">Edit</span>
                </a>
                <button
                  type="button"
                  class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu">
                  <a
                    href
                    class="dropdown-item delete-button"
                    @click.prevent="() => deleteItem(item.id)"
                  >
                    <i class="material-icons">delete</i>
                    <span class="title">Delete</span>
                  </a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
i.material-icons {
  color: inherit;
}

.action-column {
  width: 3rem;
  padding-right: 2rem;
}

.edit-btn {
  white-space: nowrap;
}
</style>
