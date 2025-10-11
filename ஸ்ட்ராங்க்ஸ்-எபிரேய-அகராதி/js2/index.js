angular.bootstrap(document, ['bookApp']);
$('.nav a').on('click', function(){
  $('.navbar-toggle').click() //bootstrap 3.x by Richard
});
document.addEventListener('DOMContentLoaded', function() {
  if (typeof $APP_NAME !== 'undefined') {
    document.getElementById('dynamic-title').innerText = $APP_NAME;
  }
  if (typeof $APP_DESCRIPTION !== 'undefined') {
    var metaDesc = document.getElementById('dynamic-description');
    if (metaDesc) metaDesc.setAttribute('content', $APP_DESCRIPTION);
  }
});