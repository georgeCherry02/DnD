<form action="" method="POST" onsubmit="return validate_spell_creation();">
    <input type="hidden" name="form_type" value="Spell"/>
    <div>
        <div class='labels_container'>
            <label for='name' class='required'>Name:</label><br/>
            <label for='level' class='required'>Level:</label><br/>
            <label for='school' class='required'>School of Magic:</label><br/>
            <label for='casting_time' class='required'>Casting Time:</label><br/>
            <label for='range_type' class='required'>Range Type:</label><br/>
            <!-- Only if range_type === "ranged" set range_distance input visible -->
            <label for='shape_type' class='required'>Shape:</label><br/>
            <!-- Only if a shape other than beam is selected allow shape_size input visible -->
            <label for='vocal'>Vocal:</label>
            <label for='somatic'>Somatic:</label>
            <!-- Figure out how to do material input -->
            <label for='concentration'>Concentration:</label>
            <label for='effect'>Effect:</label>
            <!-- Only allow damage/healing magnitude visible if effect === "damage" or "healing" -->
        </div>
    </div>
</form>