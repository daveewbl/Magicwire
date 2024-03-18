{extends file=$layout}

{block name="breadcrumb"}{/block}

{block name="content"}
    <h1 class="text-center" style="text-align: center">{$resellerGroup}</h1>
    <div id="app"></div>
    <script>
        window.resellers = {$resellers|json_encode nofilter};
        window.mapCenter = {$mapCenter|json_encode nofilter};
    </script>
    <script src="/{$vueAppJs}" defer async></script>
{/block}
