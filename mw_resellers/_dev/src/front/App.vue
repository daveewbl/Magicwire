<script>
import { useElementSize } from "@vueuse/core";

let resellers = window.resellers;
let mapCenter = window.mapCenter;

resellers = resellers.map((reseller) => {
  reseller.isVisible = true;
  return reseller;
});

export default {
  data() {
    return {
      resellers: resellers,
      mapHeight: 0,
      openedMarkerId: null,
      mapCenter: mapCenter,
    };
  },

  computed: {
    filteredResellers() {
      return this.resellers.filter((reseller) => reseller.isVisible);
    },
  },

  methods: {
    filterItems(query) {
      query = query.target.value;

      if (!query) {
        this.resellers = this.resellers.map((reseller) => {
          reseller.isVisible = true;
          return reseller;
        });
      }

      this.resellers = this.resellers.map((reseller) => {
        reseller.isVisible = reseller.name
          .toLowerCase()
          .includes(query.toLowerCase());
        return reseller;
      });
    },

    setOpenedMarkerId(id) {
      this.openedMarkerId = id;
    },

    trim(string) {
      return string.trim();
    },
  },

  mounted() {
    this.mapHeight = useElementSize(this.$refs.map.$el).height;
  },
};
</script>

<template>
  <div class="row mt-3">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-8 map-column">
          <g-map-map
            class="mt-4"
            :center="{
              lat: mapCenter.lat ? parseFloat(mapCenter.lat) : 45.5,
              lng: mapCenter.lng ? parseFloat(mapCenter.lng) : 10.8,
            }"
            :zoom="7"
            :options="{
              styles: [
                {
                  stylers: [{ hue: '#bfa279' }],
                },
              ],
            }"
            ref="map"
            map-type-id="terrain"
            style="width: 100%; aspect-ratio: 1"
          >
            <g-map-marker
              v-for="(item, key) in filteredResellers"
              :key="key"
              icon="/img/map_marker.png"
              @click="() => setOpenedMarkerId(item.id)"
              :position="{
                lat: parseFloat(item.lat),
                lng: parseFloat(item.lng),
              }"
            >
              <GMapInfoWindow :opened="openedMarkerId === item.id">
                <div>
                  <h4 class="reseller-title" v-text="item.name"></h4>
                  <p class="address">{{ item.address }}, {{ item.city }}</p>
                  <p class="mail" v-if="item.email">
                    Email:
                    <a :href="'mailto:' + trim(item.email)">
                      {{ item.email }}
                    </a>
                  </p>
                  <p class="phone" v-if="item.phone">
                    Telefono:
                    <a :href="'tel:' + trim(item.phone)">{{ item.phone }}</a>
                  </p>
                </div>
              </GMapInfoWindow>
            </g-map-marker>
          </g-map-map>
        </div>
        <div class="col-md-4">
          <div class="tile" :style="{ maxHeight: `${mapHeight}px` }">
            <div class="search-input-container">
              <input class="form-control" @input="filterItems" />
              <i class="search-icon material-icons">search</i>
            </div>
            <div
              class="mt-2 reseller-row"
              v-for="(item, key) in filteredResellers"
              :key="key"
              @click.prevent="() => setOpenedMarkerId(item.id)"
            >
              <h4 class="reseller-title" v-text="item.name"></h4>
              <p class="address">{{ item.address }}, {{ item.city }}</p>
              <p class="mail" v-if="item.email">
                <a :href="'mailto:' + trim(item.email)">
                  {{ item.email }}
                </a>
              </p>
              <p class="phone" v-if="item.phone">
                <a :href="'tel:' + trim(item.phone)">{{ item.phone }}</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2"></div>
  </div>
</template>

<style scoped>
.tile {
  background-color: #f7f7fa;
  overflow: auto;
  height: 100%;
  padding: 1rem;
}

.reseller-row {
  cursor: pointer;
  border-bottom: 1px solid rgba(0, 0, 0, 0.2);
}

.search-input-container {
  position: relative;
}

.search-input-container input {
  padding-left: 2rem;
}

.search-icon {
  position: absolute;
  left: 2%;
  top: 50%;
  transform: translateY(-50%);
}

@media (min-width: 992px) {
  .map-column {
    margin-right: -30px;
  }
}
</style>
