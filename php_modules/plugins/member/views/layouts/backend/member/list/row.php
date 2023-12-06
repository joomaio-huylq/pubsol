<tr>
    <td>
        <input class="checkbox-item" type="checkbox" name="ids[]" value="<?php echo $this->item['id']; ?>">
    </td>
    <td>
        <a href="#"
            class="show_data"
            data-id="<?php echo $this->item['id'];?>" 
            data-name="<?php echo $this->item['name'];?>" 
            data-email="<?php echo $this->item['email'];?>" 
            data-password="<?php echo $this->item['password'];?>" 
            data-bs-placement="top" 
            data-bs-toggle="modal" 
            data-bs-target="#exampleModalToggle">
            <?php echo $this->item['name'];?>
        </a>
    </td>
    <td>
        <?php echo $this->item['email'];?>
    </td>
    <td>
        <?php echo $this->item['password'];?>
    </td>
    <td>
        <?php echo $this->item['created_at'] ? date('m-d-Y', strtotime($this->item['created_at'])) : '';?>
    </td>
</tr>