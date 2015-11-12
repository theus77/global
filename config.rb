# don't use the sass bootstrap but the bower one
# require 'bootstrap-sass'
add_import_path "webroot/js/bower/bootstrap-sass/assets/stylesheets"

require 'compass/import-once/activate'
# Require any additional compass plugins here.

#sourcemap = true
#sass_options = {:debug_info => true}

# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "./webroot/css"
sass_dir = "sass"
images_dir = "./webroot/img"
javascripts_dir = "./webroot/js"
fonts_dir = "./webroot/fonts"

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed
output_style = :expanded

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
# line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass sass scss && rm -rf sass && mv scss sass
