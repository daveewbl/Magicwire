let mix = require("laravel-mix");

mix
  .js("./src/back/index.js", "./js/back.js")
  .js("./src/front/index.js", "./js/front.js")
  .vue({ version: 3 })
  .setPublicPath("../views/");
