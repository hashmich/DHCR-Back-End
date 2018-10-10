
<div class="css-columns-2">
    <h2>Downloads &amp; Information</h2>

    <div style="display: inline-block">
        <h3>Dissemination Material</h3>
        <p><?php echo $this->Html->link('Course Registry Flyer',
                '/files/DH_Course_Registry_FLyer.pdf', array(
                'target' => 'blank')); ?></p>
    </div>

    <div style="display: inline-block">
        <h3>Video Lectures</h3>
        <p><?php echo $this->Html->link('Course Registry Metadathon',
                'http://videolectures.net/DHcourse2018_paris/', array(
                    'target' => 'blank')); ?>, a workshop held during the DARIAH annual meeting, 2018 in Paris. </p>
        <p>Tutorials for using the DH Course Registry. Watch the <?php echo $this->Html->link('playlist on Youtube',
                'https://www.youtube.com/watch?v=pvFKq67-21I&list=PLlKmS5dTMgw0u-Fvz_MC_QQNkMAPeLT5E', array(
                    'target' => 'blank')); ?>.</p>
    </div>

    <div style="display: inline-block">
        <h3>API and Data Download</h3>
        <p>
            Yet, we only have a rudimentary API online, which serves either a full dump of the course database
            or a single course. The data is available as JSON or XML.<br>
            <?php echo $this->Html->link('JSON',
                '/courses/index.json', array('target' => 'blank')); ?><br>
            <?php echo $this->Html->link('XML',
                '/courses/index.json', array('target' => 'blank')); ?><br>
            To retrieve a single course only, the schema is as follows, where [format] has to be either json or xml:
            <?php echo Router::url('/courses/view/[id].[format]'); ?>.<br>
            <?php echo $this->Html->link('Single course example',
                '/courses/view/123.json', array('target' => 'blank')); ?>.
        </p>

    </div>

</div>

