<?php
global $imgpath;
?>
<style>
    .wrap img {
        max-width: 600px;
        width: 100%;
        display: block;
    }
</style>
<h1>How to attain a Google Maps API key</h1>
<p>Setting up Google Reviews with <strong>Embedder for Google Reviews</strong> is simple but before we begin, first make sure you have an API key from Google.</p>

<h3>Create a project or choose an existing</h3>
<p>Google Reviews integration uses the Google Maps API to fetch the reviews and display them. Let us learn how to fetch the API Key.</p>

<ol>
    <li>Go to the <a href="https://console.developers.google.com/apis/dashboard" target="_blank">Google API Console</a>.</li>
    <li>If you've never worked with Google API Console, you need to add your payment data first</li>
    <ol>
        <li>If you do not do this, the API will not work. You will <strong>not be able</strong> to pull any reviews</li>
        <li>You will receive a credit that probably will never be used at all.</li>
        <li>The API requests are so rare, it will almost never cause any costs per month (a few cents, if any)</li>
        <li>You will be asked to give your company information, phone number and payment data as well.</li>
    </ol>
    <li>Create a new project or select a project from the <strong>Select the Project</strong> dropdown.</li>
    <li>After creating the project click <strong>Enabled APIs and Services</strong> option on the project page.</li>
</ol>

<h3>Select the Maps JavaScript API</h3>

<ol start="4">
    <li>Select the <strong>Maps JavaScript API</strong> from the APIs list and click the '<strong>Enable</strong>' button.</li>
</ol>

<h3>Select the Places API</h3>
<ol start="5">
    <li>Go back to API Library and select <strong>Places API</strong> and enable it as well.</li>
</ol>

<h3>Create credentials</h3>
<ol start="6">
    <li>After enabling the APIs, select the APIs and Services option and then the <strong>Credentials</strong> option from the sidebar.</li>
</ol>

<ol start="6">
    <li>On the next page, select <strong>Create Credentials</strong> >&gt; <strong>API Key</strong></li>
</ol>

<ol start="7">
    <li>A modal box will appear with the newly created API key.</li>
    <li>Copy the API key and paste it in a text file or somewhere else to keep it for the next steps</li>
</ol>

<blockquote><strong>NOTE:</strong> You should restrict the API key, so it can only be used by your domain.</blockquote>

<h3>Adding the API key to your Embedder for Google Reviews options</h3>
<ol start="8">
    <li>Now, head back to the '<strong>Embedder for Google Reviews</strong>' options panel in your WordPress backend.</li>
    <li>Paste the API key in the API key field.</li>
</ol>

<h3>Look up your business</h3>
<ol start="8">
    <li>Then, type in the name of your business (Only local businesses will appear, service based businesses are not allowed from Googles side)</li>
    <li>Copy the <strong>Place ID</strong></li>
    <li>Paste the <strong>Place ID</strong> into the text field</li>
    <li>If for any reasons, this should not be working as expected, please go to <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank"> this site</a> and try again there.</li>
</ol>

<h3>Hit 'Save Changes' - Done!</h3>

<ul>
    <li>From now on, your reviews get pulled automatically, once a day</li>
</ul>
