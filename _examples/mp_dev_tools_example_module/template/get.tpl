<div>

    <table>
    {foreach $data as $key => $entry}
        <tr>
            <td style="padding: 5px 10px">
                <pre><strong>{$entry.key}</strong></pre>
            </td>
            <td style="padding: 5px 10px">
                <pre>{$entry.value}</pre>
            </td>
        </tr>
    {/foreach}
    </table>
</div>