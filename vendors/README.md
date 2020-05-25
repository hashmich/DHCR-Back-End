# phpUnit 3.7.38 fork for cakePhp 2.x development under php >= 7.2.x

While setting up the test framework as suggested in the 
[cakePhp cookbook](https://book.cakephp.org/2.0/en/development/testing.html), 
following problems occured: 

* phpUnit versions 4.x | 5.x seemed not to be working with cake's implementation of the testing framework
  -> downgrading phpUnit to the latest 3.x -> 3.7.38
* running tests on php versions >= 7.2 fails du to E_STRICT fatal errors
* method declaration footprints of phpUnit Comparator do not comply with the E_STRICT requirements
* Comparator has been patched at a [later version](https://github.com/sebastianbergmann/comparator/pull/30/commits/f4ec922b3e514ae27d2c9693bc68222a93ceab34), but the patch is only available recent branches, not for the 3.7 branch

phpUnit 3.7.38 has been written before the E_STRICT error ever existed or lead to fatal errors. 
Thus, upgrading to a more recent php version requires custom patches to the test framework. 

This repositroy holds all required dependencies for testing under cakePhp 2.x (tested 2.10), to be installed in project_dir/vendors. 

Submodule installation: 

git submodule add https://github.com/hashmich/phpUnit.git vendors
