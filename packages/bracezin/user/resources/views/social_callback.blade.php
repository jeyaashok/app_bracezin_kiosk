<!doctype html>
<html>
<head><meta charset="utf-8"></head>
<body>
<script>
  (function(){
    const payload = {
      source: 'social-auth',
      state: '{{ $state }}',
      token: '{{ $token }}',
      data: {!! json_encode($user) !!}
    };
    try {
      // Post to opener with explicit allowed origin
      const allowed = '{{ $frontend }}' || '*';
      if (window.opener && !window.opener.closed) {
        window.opener.postMessage(payload, allowed);
      }
      // Storage fallback
      try { localStorage.setItem('social_auth_{{ $state }}', JSON.stringify(payload)); } catch(e){}
    } catch(e){}
    window.close();
  })();
</script>
</body>
</html>