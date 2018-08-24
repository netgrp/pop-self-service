<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "EmailMessage",
  "potentialAction": {
    "@type": "ConfirmAction",
    "url": "{{ $url }}",
    "name": "{{ $slot }}"
  },
  "description": "{{ $description }}"
}
</script>