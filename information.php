
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />

<div class="items">
    
<h1 class="titles"><?php echo $falcon_weather_profile->{'list'}[$i-1]->{'name'};?></h1>
<h4 class="degrees"><b><?php echo $falcon_weather_profile->{'list'}[$i-1]->{'main'}->{'temp'};?> &deg;</b> </h4>
    <h3 class="description"><?php echo $falcon_weather_profile->{'list'}[$i-1]->{'weather'}[0]->{'description'};?> </h3>

<?php if($display_wind_speed == '1') :?>
<p class="wind">Wind speed: <?php echo $falcon_weather_profile->{'list'}[$i-1]->{'wind'}->{'speed'};?> m/s</p>
<?php endif; ?>

<?php if($display_cloud_coverage == '1') :?>
<p class="clouds"> Cloud coverage: <?php echo $falcon_weather_profile->{'list'}[$i-1]->{'clouds'}->{'all'};?> % </p>
<?php endif; ?>
    
   </div> 
