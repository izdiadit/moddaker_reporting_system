// Prepare Moddaker Countries dataset:


am5.ready(function() {

    // Create root_map element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root_map = am5.Root.new("chartdiv4");
    
    document.getElementById("chartdiv4").style.height = 500;
    
    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root_map.setThemes([
      am5themes_Animated.new(root_map)
    ]);
    
    
    // Create the map chart
    // https://www.amcharts.com/docs/v5/charts/map-chart/
    var chart = root_map.container.children.push(am5map.MapChart.new(root_map, {
      panX: "translateX",
      panY: "translateY",
      projection: am5map.geoMercator()
    }));
    
    
    // Create main polygon series for countries
    // https://www.amcharts.com/docs/v5/charts/map-chart/map-polygon-series/
    var polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root_map, {
      geoJSON: am5geodata_worldLow,
      geodataNames: am5geodata_lang_AR,
      fill: am5.color(0xc5c7c7),
      exclude: ["AQ"],
    }));
    
    var polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root_map, {
        geoJSON: am5geodata_worldLow,
      geodataNames: am5geodata_lang_AR,
      fill: am5.color(0x977c47),
      include: country_data,
      exclude: ["AQ"],
    }));
    polygonSeries.mapPolygons.template.setAll({
    //   tooltipText: "{name}",
      tooltipHTML: "<div class='customTooltip'>{name}</div>",
      toggleKey: "active",
      interactive: true
    });
    
    polygonSeries.mapPolygons.template.states.create("hover", {
      fill: root_map.interfaceColors.get("positive")
      // https://www.amcharts.com/docs/v5/concepts/colors-gradients-and-patterns/#Interface_colors
    });
    
    polygonSeries.mapPolygons.template.states.create("active", {
      fill: root_map.interfaceColors.get("positive")
    });
    
    var previousPolygon;
    
    polygonSeries.mapPolygons.template.on("active", function (active, target) {
      if (previousPolygon && previousPolygon != target) {
        previousPolygon.set("active", false);
      }
      if (target.get("active")) {
        polygonSeries.zoomToDataItem(target.dataItem );
      }
      else {
        chart.goHome();
      }
      previousPolygon = target;
    });
    
    
    // Add zoom control
    // https://www.amcharts.com/docs/v5/charts/map-chart/map-pan-zoom/#Zoom_control
    chart.set("zoomControl", am5map.ZoomControl.new(root_map, {}));
    
    
    // Set clicking on "water" to zoom out
    chart.chartContainer.get("background").events.on("click", function () {
      chart.goHome();
    })
    

    // Add Custom Dataset:
    // polygonSeries.data.setAll([{
    //     id: "SD",
    //     polygonSettings: {
    //         fill: am5.color(0x977c47)
    //     }
    // }, {
    //     id: "EG",
    //     polygonSettings: {
    //         fill: am5.color(0x977c47)
    //     }
    // }]);
    
    // Make stuff animate on load
    chart.appear(1000, 100);
    
    }); // end am5.ready()