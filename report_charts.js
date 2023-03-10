// Instantiating the chart:
var root = am5.Root.new("chartdiv");
var chart = root.container.children.push(
    am5percent.PieChart.new(root, {layout: root.verticalLayout})
);

// // Hide the amCharts logo
// if (chart.logo) {
//     chart.logo.disabled = true;
// }
// chart.logo.disabled = true;

// Adding series. Pie chart supports one series type: PieSeries
var myseries = chart.series.push(
  am5percent.PieSeries.new(root, {
    name: "Series",
    categoryField: "type",
    valueField: "count",
    legendLabelText: "[fontFamily: calibri]       {category}: {valuePercentTotal.formatNumber('0.00')}%[/]", //Add the value with the category and empty the value label to avoid arabic lang overlapping
    legendValueText: "",
    tooltip: am5.Tooltip.new(root, {
        labelHTML: "<div class='customTooltip'>{category}: {valuePercentTotal.formatNumber('0.0')}%</div>"
    })
  })
  );
  // Legend:
  var legend = chart.children.push(am5.Legend.new(root, {
   centerX: am5.percent(30),
   x: am5.percent(60),
   centerY: am5.percent(100),
   y: am5.percent(100),
   layout: root.gridLayout
  }));


// // Hiding tooltips:
// myseries.slices.template.setAll({ })


// set themes:
root.setThemes([
    am5themes_Animated.new(root)
]);

// Styling labels:
myseries.labels.template.setAll({
    // maxWidth: 150,
    // oversizedBehavior: "wrap" // to truncate labels, use "truncate"
    text: window.innerWidth >= 1218? "[fontFamily: calibri]{category}: {valuePercentTotal.formatNumber('0.0')}%[/]" : "",
    radius: 10,
    inside: true,
    textType: "radial", centerX: am5.percent(100),
  });

  myseries.ticks.template.setAll({
    location: 1
  });

// animation
myseries.animate({
    key: "startAngle",
    from: 270,
    to: 630,
    loops: 1,
    duration: 2000,
    easing: am5.ease.inOut(am5.ease.cubic) // linear (Constant speed during all duration) - circle - cubic elastic - ...
});

// Colors: Wherever you need to specify a color in amCharts 5 you need to pass in a Color object.
/* A color set comes with a pre-defined list of colors, depending on the theme we are using (if any).

There is a number of ways to override the list as needed.

The most easiest way is to simply set its colors setting to an array of Color objects as done below. Another option is to modify default theme.
*/
// myseries.set("fill", am5.color('#ff0000'));

myseries.slices.template.adapters.add("stroke", () => 'aliceblue');


myseries.data.setAll(types_data);
legend.data.setAll(myseries.dataItems);
myseries.appear(1000);
chart.appear(1000, 1000);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (document.body.contains(document.getElementById("chartdiv3"))) {
    // check if admin div exists, to avoid errors for nonadmin sessions
	var root2 = am5.Root.new("chartdiv3");
	var chart2 = root2.container.children.push(
    am5percent.PieChart.new(root2, {
      layout: root2.verticalLayout,
      innerRadius: am5.percent(60),
	    })
	);
	
	// Adding series. Pie chart supports one series type: PieSeries
	var myseries2 = chart2.series.push(
	    am5percent.PieSeries.new(root2, {
	        name: "Series",
            categoryField: "country",
            valueField: "count",
            legendLabelText: "[fontFamily: calibri]       {category}: {valuePercentTotal.formatNumber('0.0')}%[/]", //Add the value with the category and empty the value label to avoid arabic lang overlapping
            legendValueText: "",
            tooltip: am5.Tooltip.new(root2, {
                labelHTML: "<div class='customTooltip'>{category}: {valuePercentTotal.formatNumber('0.0')}%</div>"
            })
	    })
	);
	
   // Legend:
   var legend = chart2.children.push(am5.Legend.new(root2, {
    centerX: am5.percent(30),
    x: am5.percent(60),
    centerY: am5.percent(100),
    y: am5.percent(100),
    layout: am5.GridLayout.new(root2, {
      maxColumns: 2,
      fixedWidthGrid: false
    })
   }));

    legend.itemContainers.template.setup = function (item) {
        item.events.disableType("pointerover")
    };

	root2.setThemes([
    am5themes_Responsive.new(root2)
	]);

    // Styling labels:
    myseries2.labels.template.setAll({
        // maxWidth: 150,
        // oversizedBehavior: "wrap" // to truncate labels, use "truncate"
        fontsize: 20,
        text: "[fontFamily: calibri]{category}: {valuePercentTotal.formatNumber('0.0')}%[/]",
      });

	
	// Coloring one by one:
	// myseries2.slices.template.adapters.add("fill", function(fill, target) {
	//     switch (myseries2.slices.indexOf(target)) {
	//         case 0:
	//             return 'darkgoldenrod';
	//             break;
	//         case 1:
	//             return 'green';
	//             break;
	//         default:
	//             return 'blue';
	//             break;
	//     }
	//   });
	
	// myseries2.animate({
	//     key: "startAngle",
	//     from: 270,
	//     to: 630,
	//     loops: 1,
	//     duration: 3000,
	//     easing: am5.ease.inOut(am5.ease.cubic) // linear (Constant speed during all duration) - circle - cubic elastic - ...
	// });
	myseries2.slices.template.adapters.add("stroke", () => 'whitesmoke'); //same as chartCard background
	myseries2.slices.template.adapters.add("strokeWidth", () => 5);
	myseries2.data.setAll(country_data);
	legend.data.setAll(myseries2.dataItems);

    myseries2.appear(1000,1000);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
am5.ready(function() {
var root3 = am5.Root.new("chartdiv3");
var chart3 = root3.container.children.push(
    am5xy.XYChart.new(root3, {

    })
);

root3.setThemes([
    am5themes_Animated.new(root3)
]);

// Create axes:
var xrend = am5xy.AxisRendererX.new(root3, {
    minGridDistance: 1,
    centerY: am5.p50,
    centerX: am5.p100,
});
xrend.labels.template.setAll({
    rotation: -15,
})
var xaxis = chart3.xAxes.push(am5xy.CategoryAxis.new(root3, {
    maxDeviation: 0.3,
    renderer: xrend,
    categoryField: "country",
    tooltip: am5.Tooltip.new(root3, {})
}));

var yrend = am5xy.AxisRendererY.new(root3, {});
yrend.labels.template.setAll({
    // rotation: -30,
    paddingLeft: 30,
});
var yaxis = chart3.yAxes.push(am5xy.ValueAxis.new(root3, {
    renderer: yrend,
}));

// Create series:
var myseries3 = chart3.series.push(am5xy.ColumnSeries.new(root3,{
    name: 'sereies',
    xAxis: xaxis,
    yAxis: yaxis,
    categoryXField: "country",
    valueYField: "count",
}));

// Column SIZE and border radius:
myseries3.columns.template.setAll({
    cornerRadiusTR: 30,
    cornerRadiusTL: 30,
    width: 35
})

//coloring:
// myseries3.columns.template.adapters.add("fill", () => 'darkgoldenrod');
// myseries3.columns.template.adapters.add("stroke", () => 'green');

myseries3.columns.template.adapters.add("fill", function(fill, target) {
  return chart3.get("colors").getIndex(myseries3.columns.indexOf(target));
});

myseries3.columns.template.adapters.add("strokeWidth", () => 3);

// country_data_for_xy = [];
// for (const e in country_data) {
//         const temp = country_data[e];
//         temp.country = country_data[e].country
//         temp.count = Number(country_data[e].count)
//         country_data_for_xy.push(temp)
// }
// console.log(country_data_for_xy);

// Styling the labels:
xaxis.get("renderer").labels.template.setAll({
    oversizedBehavior: "wrap",
    maxWidth: 100,
    textAlign: "center",
  });


xaxis.data.setAll(country_data);
myseries3.data.setAll(country_data);

myseries3.appear(1000);
chart3.appear(1000, 100);
});
*/

////////////////////////////////////////////////////////////////////////////////////////////////////////

/* WARNING 1:  It's a good practice to make sure that setting data happens as late into code as possible. Once you set data, all related objects are created, 
so any configuration settings applied afterwards might not carry over.

WARNING 2: 
// ERROR: the following will result in error
var root = new am5.Root("chartdiv");
// SUCCESS: this is correct
var root = am5.Root.new("chartdiv");
This true not just for Root but for every single class in amCharts 5.

WARNING 3:
root.dispose();
Trying to create a new root element in a <div> container before disposing the old one that is currently residing there, will result in an error.
*/

