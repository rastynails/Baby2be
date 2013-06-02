{container stylesheet="profile_status_line.style"}
         
        <div class="status">
            <span class="status_label">{text %.components.profile_status.profile_status_label}:</span>
            <span class="status_value">{$status}</span> 
	    |
        </div>
	
        <div class="membership">
            <span class="membership_label">{text %.components.profile_status.membership_label}:</span>
            <span class="membership_value"><a href="{document_url doc_key=payment_selection}">{text %.membership.types.`$membership.membership_type_id`}</span></a>
        </div>
    
{/container}