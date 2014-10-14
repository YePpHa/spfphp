module.exports = function(grunt) {
  function buildFile() {
    var phpStart = "<?php";
    var phpEnd = "?>";
    var linebreak = "\r\n";
    var content = "";
    var license = "/**" + linebreak + grunt.file.read("./LICENSE") + linebreak + "**/";
    
    content += phpStart + linebreak + linebreak + license + linebreak + linebreak;
    
    grunt.file.recurse("./src/", function(abspath, rootdir, subdir, filename){
      var file = grunt.file.read(abspath);
      file = file.replace(/[\r\n]*((\<\?php)|(\?\>))[\r\n]*/g, "");
      
      content += file + linebreak + linebreak;
    });
    
    content += phpEnd;
    
    grunt.file.write("./build/spf.php", content);
  }
  
  grunt.registerTask("default", "Build the php file.", buildFile);
  grunt.registerTask("build", "Build the php file.", buildFile);
};