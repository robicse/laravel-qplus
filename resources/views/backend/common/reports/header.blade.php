<table>
    <tr>
        <td style="width: 15%;float:left;padding-right:1%">
            <img loading="" src="{{ asset(@$default_business_settings[4]->value) }}" height="140" width="140"
                style="">
        </td>
        <td style="width: 75%;float: right;" class="text-left" colspan="3" >
            <strong>{{ @$default_business_settings[0]->value }}</strong><br>
            {{-- <strong>ওয়াটার ফাইন</strong><br> --}}
            <strong>Water Fine</strong><br>
            {{  @$default_business_settings[5]->value }}<br>
            Mobile: {{  @$default_business_settings[1]->value }}<br>
            Email: {{  @$default_business_settings[2]->value }}<br>
            Website: {{  @$default_business_settings[6]->value }}<br>
            Address: {{ @$default_business_settings[3]->value }}<br>
        </td>
    </tr>
</table>
