<html>
	<head>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAyfq5GA5XtPTfFt5qiSmKZxSMUXt0jUOiqB1vk7nw8kTkvRwYPhSNKRWiJ74U6NVzx2izClqmn9S0nw" type="text/javascript"></script>
<script type="text/javascript">
    function initialize() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng(37.4419, -122.1419), 13);
        map.setUIToDefault();
      }
    }

    </script>	
	</head>
	<body onload="initialize()" onunload="GUnload()">
	test
	<div id="map_canvas" style="width: 500px; height: 300px"></div>
	</body>
</html>
