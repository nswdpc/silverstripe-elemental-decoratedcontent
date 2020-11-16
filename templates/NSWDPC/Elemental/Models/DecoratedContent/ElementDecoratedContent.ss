<div class="{$ElementStyles}">
    <% if $LinkTarget %><a href="{$LinkURL}" title="{$Title.XML}"<% if $OpenInNewWindow %> target="_blank"<% end_if %>><% end_if %>
    <% if $Tags %>
    <div class="tags">
        <% loop $Tags %>
            <span>{$Name.XML}</span>
        <% end_loop %>
    </div>
    <% end_if %>
    <% if $PublicDate %>
    <div class="date">{$PublicDate.Nice}</div>
    <% end_if %>
    <div class="content">
	    <% if $ShowTitle %>
            <% if $Title %>
                <h2 class="content-element__title">{$Title.XML}</h2>
            <% else_if $CallToAction %>
                <h2 class="content-element__title">{$CallToAction.XML}</h2>
            <% end_if %>
        <% end_if %>
	    <% if $Subtitle %>
            <h3 class="content-element__subtitle">{$Subtitle.XML}</h3>
        <% end_if %>
        <% if $HTML %>
            $HTML
        <% end_if %>
    </div>
    <% if $Image %>
        <div class="image">
            {$Image.ScaleWidth(960)}
        </div>
    <% end_if %>
    <% if $LinkTarget %></a><% end_if %>
</div>
