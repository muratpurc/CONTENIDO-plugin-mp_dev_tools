<div>

    <table>
    {foreach $data as $key => $entry}
        <tr>
            <td style="padding: 5px 10px; text-align: right;">
                <pre><strong>{$entry.key}</strong></pre>
            </td>
            <td style="padding: 5px 10px">
                <pre>{$entry.value}</pre>
            </td>
        </tr>
        {if isset($entry.format)}
            <tr>
                <td style="padding: 5px 10px; text-align: right;">
                    <pre>{$lblFormatIs}:</pre>
                </td>
                <td style="padding: 5px 10px">
                    <pre>{$entry.format}</pre>
                </td>
            </tr>
        {/if}
        {if isset($entry.values) && is_array($entry.values)}
            <tr>
                <td style="padding: 5px 10px; text-align: right;">
                    <pre>{$lblValues}:</pre>
                </td>
                <td style="padding: 5px 10px">
                    <pre>{$entry.values|print_r:true}</pre>
                </td>
            </tr>
        {/if}
        <tr><td colspan="2"><hr></td></tr>
    {/foreach}
    </table>
</div>