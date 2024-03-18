{extends file=$layout}

{block name="breadcrumb"}{/block}

{block name="content"}
    <h1 class="text-center">{$resellerGroup}</h1>
    {$resellers|dump}
{/block}
