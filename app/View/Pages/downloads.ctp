
<div class="css-columns-2">
    <h2>Downloads &amp; Information</h2>

    <div style="display: inline-block">
        <h3>Dissemination Material</h3>
        <p><?php echo $this->Html->link('Course Registry Flyer',
                '/files/DH_Course_Registry_FLyer.pdf', array(
                'target' => '_blank')); ?></p>
    </div>

    <div style="display: inline-block">
        <h3>Video Lectures</h3>
        <p><?php echo $this->Html->link('Course Registry Metadathon',
                'http://videolectures.net/DHcourse2018_paris/', array(
                    'target' => '_blank')); ?>, a workshop held during the DARIAH annual meeting, 2018 in Paris. </p>
        <p>Tutorials for using the DH Course Registry. Watch the <?php echo $this->Html->link('playlist on Youtube',
                'https://www.youtube.com/watch?v=pvFKq67-21I&list=PLlKmS5dTMgw0u-Fvz_MC_QQNkMAPeLT5E', array(
                    'target' => '_blank')); ?>.</p>
    </div>

    <div style="display: inline-block">
        <h3>API and Data Download</h3>
        <p>
            Yet, we have a rudimentary API online, which serves either a full dump of the course database
            or a single course. The data is available as JSON or XML.<br>
            <?php echo $this->Html->link('JSON',
                '/courses/index.json', array('target' => '_blank')); ?><br>
            <?php echo $this->Html->link('XML',
                '/courses/index.json', array('target' => '_blank')); ?><br>
            To retrieve a single course only, the schema is as follows, where [format] has to be either json or xml:
            <?php echo Router::url('/courses/view/[id].[format]'); ?>.<br>
            <?php echo $this->Html->link('Single course example',
                '/courses/view/123.json', array('target' => '_blank')); ?>.
        </p>


    </div>

    <div style="display: inline-block">
        <h3>iFrame Embedding</h3>
        <p>
            The course registry can be embedded into any other website by using an iframe.
            The additional route parameter "iframe" in the URL string will cause the layout to render
            without the upper logo bar and tabbing menu.
            Thus, it will integrate seamlessly into foreign institution pages.
        </p>
        <p>
            The basic URL for iframe embedding is:<br>
            <?php echo $this->Html->link(Router::url('/iframe', $full = true), '/iframe'); ?>
        </p>
        <p>
            Using the provided code snippet beyond, the iframe will expand to 100% of the parent container.
            The height is adjusted by some javascript from the page loaded within the iframe,
            thus you have to keep the provided id.
            Embedding code:
        </p>
        <p>
            <code>
                &lt;iframe <br>
                src="<?php echo Router::url('/iframe', $full = true); ?>"<br>
                width="100%" id="dhcr-iframe"/&gt;
            </code>
        </p>
    </div>
    <div style="display:inline-block">
        <h3>URL Filter Parameters</h3>
        <p>
            You can also pass filter parameters via the URL, which is handy to filter for a specific country or
            discipline in the iframe-embedded application or to provide a link with preset filter settings.
            By adding <em>[key:value]</em> pairs to the end of the URL string, the results will be filtered
            for a particular numeric ID-value of an attribute field.
            Be aware, that you have to provide the full <em>[/controller/action]</em> combo in that case: <br>
            <?php echo $this->Html->link(Router::url('/iframe/courses/index/country_id:1', $full = true), '/iframe/courses/index/country_id:1'); ?>
        </p>
        <p>
            The available filter parameters are:
        </p>
        <ul>
            <li><em>country_id</em></li>
            <li><em>city_id</em></li>
            <li><em>institution_id</em></li>
            <li><em>course_type_id</em> (Education)</li>
            <li><em>tadirah_object_id</em></li>
            <li><em>tadirah_technique_id</em></li>
            <li><em>discipline_id</em></li>
        </ul>
        <p>
            You can lookup the id of any attribute of interest
            with any HTML inspection tool in the filter form on the leftmost side on the homepage.
            Otherwise, you may send us an email and ask for help.
        </p>
    </div>

</div>

