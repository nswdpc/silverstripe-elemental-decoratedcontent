<%-- card layout for decorated content (list context) --%>
<div class="{$ElementStyles}">
    <div class="content">
	    <% if $Title && $ShowTitle %>
            <h2 class="content-element__title">{$Title.XML}</h2>
        <% end_if %>
        <% if $CallToAction %>
	          {$CallToAction.XML}
        <% end_if %>
    </div>
    <% if $Image %>
        <div class="image">
            {$Image.Fill(420,320)}
        </div>
    <% end_if %>
    <% if $Link %>
        <div class="link">
        <% with $Link %>
            <p><a href="$LinkURL">$Title.XML</a></p>
        <% end_with %>
        </div>
    <% end_if %>
</div>
