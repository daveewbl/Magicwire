<script>
import axios from "axios";
import SelectField from "../components/SelectField.vue";
import InputSwitch from "../components/InputSwitch.vue";
import InputField from "../components/InputField.vue";

const itemSkeleton = window.itemSkeleton;
const urls = window.postUrls;
const resellerGroups = window.resellerGroups;

export default {
  components: { SelectField, InputSwitch, InputField },

  emits: ["changeView"],

  data() {
    return {
      item: this.currentlyEditing ?? itemSkeleton,
      name: { ...this.currentlyEditing }.name ?? "Nuovo rivenditore",
      resellerGroups: resellerGroups,
      error: null,
      mapZoom: 7,
    };
  },

  methods: {
    save() {
      if (!this.validate()) {
        return;
      }

      axios
        .post(urls.createOrUpdate, this.item)
        .then(({ data }) => {
          this.setItems(data.items);
          this.setCurrentlyEditing(null);
          this.setCurrentView("List");
        })
        .catch(
          ({ response }) =>
            (this.error = `${response.status} - ${response.data}`)
        );
    },

    validate() {
      const fields = Object.entries(this.$refs).filter(([key]) =>
        key.startsWith("fields.")
      );

      return fields.every((field) => {
        field = field[1];
        if (!field.getFieldElement().checkValidity()) {
          field.setError(true);

          return false;
        }
        field.setError(false);

        return true;
      });
    },

    deleteItem() {
      if (!confirm(`Vuoi veramente eliminare "${this.item.name}"?`) === true) {
        return;
      }

      axios
        .post(urls.delete, { id: this.item.id })
        .then(({ data }) => {
          this.setItems(data.items);
          this.setCurrentlyEditing(null);
          this.setCurrentView("List");
        })
        .catch(
          ({ response }) =>
            (this.error = `${response.status} - ${response.data}`)
        );
    },

    setPlace(place) {
      this.item.name = place.name;

      this.item.lat = place.geometry.location.lat();
      this.item.lng = place.geometry.location.lng();

      const address = this.getAddressComponent(
        place.address_components,
        "route"
      );

      const streetNumber = this.getAddressComponent(
        place.address_components,
        "street_number"
      );

      const postalCode = this.getAddressComponent(
        place.address_components,
        "postal_code"
      );

      const locality = this.getAddressComponent(
        place.address_components,
        "locality"
      );

      const amLevel2 = this.getAddressComponent(
        place.address_components,
        "administrative_area_level_2"
      );

      if (address) {
        this.item.address = address.long_name;
      }

      if (streetNumber) {
        this.item.address += `, ${streetNumber.long_name}`;
      }

      if (postalCode) {
        this.item.city = postalCode.long_name;
      }

      if (locality) {
        this.item.city += ` ${locality.long_name}`;
      }

      if (amLevel2) {
        this.item.city += ` ${amLevel2.short_name}`;
      }

      if (place.formatted_phone_number) {
        this.item.phone = place.formatted_phone_number;
      }

      this.mapZoom = 12;
    },

    getAddressComponent(components, type) {
      const result = components.find((component) =>
        component.types.includes(type)
      );

      return result === "undefined" ? null : result;
    },
  },

  inject: [
    "setCurrentlyEditing",
    "setCurrentView",
    "currentlyEditing",
    "setItems",
  ],
};
</script>

<template>
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col">
          <h1 class="title" v-text="name"></h1>
        </div>
        <div class="col-auto">
          <button
            class="btn btn-danger ml-2"
            @click.prevent="() => deleteItem()"
          >
            <i class="material-icons">delete</i>
            Elimina
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
      <div class="row">
        <div class="col-6">
          <input-field
            v-model="item.name"
            required="true"
            label="Nome"
            ref="fields.name"
          ></input-field>
          <input-field
            v-model="item.address"
            label="Indirizzo"
            ref="fields.address"
          ></input-field>
          <input-field
            v-model="item.city"
            label="Città"
            ref="fields.city"
          ></input-field>
          <input-field
            v-model="item.phone"
            label="Telefono"
            ref="fields.phone"
          ></input-field>
          <input-field
            v-model="item.email"
            label="Email"
            ref="fields.email"
          ></input-field>
          <input-field
            v-model="item.lat"
            label="Latitudine"
            ref="fields.lat"
          ></input-field>
          <input-field
            v-model="item.lng"
            label="Longitudine"
            ref="fields.lng"
          ></input-field>
          <select-field
            label="Gruppo Rivenditori"
            v-model="item.group.id"
            required="true"
            ref="fields.group"
          >
            <option
              v-for="(group, key) in resellerGroups"
              :key="key"
              :value="group.id"
              v-text="group.name"
            ></option>
          </select-field>
          <input-switch v-model="item.active" label="Attivo"></input-switch>
        </div>
        <div class="col-6">
          <label>Cerca un luogo</label>
          <g-map-autocomplete
            class="form-control"
            placeholder="Cerca"
            @place_changed="setPlace($event)"
          ></g-map-autocomplete>
          <g-map-map
            class="mt-4"
            :center="{
              lat: isNaN(parseFloat(item.lat))
                ? 45.5502746
                : parseFloat(item.lat),
              lng: isNaN(parseFloat(item.lng))
                ? 11.5097152
                : parseFloat(item.lng),
            }"
            :zoom="mapZoom"
            map-type-id="terrain"
            style="width: 100%; height: 30rem"
          >
            <g-map-marker
              @click="() => (mapZoom = 13)"
              :position="{
                lat: parseFloat(item.lat),
                lng: parseFloat(item.lng),
              }"
            ></g-map-marker>
          </g-map-map>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <div class="row">
        <div class="col text-right">
          <button
            class="btn btn-secondary"
            @click="
              () => {
                setCurrentlyEditing(null);
                setCurrentView('List');
              }
            "
          >
            Cancella
          </button>
          <button class="btn btn-primary ml-2" @click.prevent="save">
            Salva
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.title {
  padding: 0.3125rem 0.3125rem 0.3125rem 0;
  margin-bottom: 0;
  font-size: 1.625rem;
  font-weight: 400;
}
i.material-icons {
  color: inherit;
}
</style>
