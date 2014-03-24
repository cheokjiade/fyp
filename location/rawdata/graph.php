<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
$session_hash = 'ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3';
//$query = $conn->prepare("SELECT locationpoint_id FROM locationpoint;");
$query = $conn->prepare("SELECT * FROM locationpoint WHERE locationpoint_id IN (SELECT locationpoint_id FROM stoppoint WHERE session_hash = :session_hash GROUP BY locationpoint_id);");
$query->bindParam(":session_hash",$session_hash);
$query->execute();
$nodes = $query->fetchAll(PDO::FETCH_ASSOC);


//$query = $conn->prepare("SELECT sp1.locationpoint_id as routestart, sp2.locationpoint_id as routeend FROM route r, stoppoint sp1, stoppoint sp2 WHERE r.stoppoint_id_start = sp1.stoppoint_id AND r.stoppoint_id_end = sp2.stoppoint_id;");
$query = $conn->prepare("SELECT sp1.locationpoint_id as routestart, sp2.locationpoint_id as routeend FROM route r, stoppoint sp1, stoppoint sp2 WHERE r.session_hash = :session_hash AND r.stoppoint_id_start = sp1.stoppoint_id AND r.stoppoint_id_end = sp2.stoppoint_id;");
$query->bindParam(":session_hash",$session_hash);
$query->execute();
$edges = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta name="description" content="[An example of getting started with Cytoscape.js]" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<meta charset=utf-8 />
<title>Cytoscape.js initialisation</title>
  <script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>
<style>
body { 
  font: 14px helvetica neue, helvetica, arial, sans-serif;
}

#cy {
  height: 100%;
  width: 100%;
  position: absolute;
  left: 0;
  top: 0;
}
</style>
</head>
<body>
  <div id="cy"></div>
<script>
$(function(){ // on dom ready

$('#cy').cytoscape({
  style: cytoscape.stylesheet()
    .selector('node')
      .css({
        'content': 'data(name)',
        'text-valign': 'center',
        'color': 'white',
        'text-outline-width': 2,
        'text-outline-color': '#888'
      })
    .selector('edge')
      .css({
        'target-arrow-shape': 'triangle'
      })
    .selector(':selected')
      .css({
        'background-color': 'black',
        'line-color': 'black',
        'target-arrow-color': 'black',
        'source-arrow-color': 'black'
      })
    .selector('.faded')
      .css({
        'opacity': 0.25,
        'text-opacity': 0
      }),
    layout: { name: 'circle' /* , ... */ },
  
  elements: {
    nodes: [
    <?php foreach($nodes as $node){?>
        {data: { id: '<?php echo $node['locationpoint_id'];?>', name: '<?php echo $node['locationpoint_id'];?>'} },
    <?php
    }
     ?>
    ],
    edges: [
        <?php foreach($edges as $edge){?>
        {data: { source: '<?php echo $edge['routestart'];?>', target: '<?php echo $edge['routeend'];?>'} },
        <?php
        }
         ?>
    ]
  },
  
  ready: function(){
    window.cy = this;
    
    // giddy up...

    cy.elements().unselectify();
    
    cy.on('tap', 'node', function(e){
      var node = e.cyTarget; 
      var neighborhood = node.neighborhood().add(node);
      
      cy.elements().addClass('faded');
      neighborhood.removeClass('faded');
    });
    
    cy.on('tap', function(e){
      if( e.cyTarget === cy ){
        cy.elements().removeClass('faded');
      }
    });
  }
});

}); // on dom ready
</script>
</body>
</html>