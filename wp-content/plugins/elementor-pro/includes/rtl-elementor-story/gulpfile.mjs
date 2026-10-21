import gulp from 'gulp'
import gulpZip from 'gulp-zip'
import gulpRename from 'gulp-rename'
import { homedir } from 'os'

const userHomeDirectory = homedir()
const desktopDir = `${userHomeDirectory}/Desktop`

export const zip = () => {
  return gulp
    .src(
      [
        '**/*',
        '!node_modules/**',
        '!.idea/**',
        '!vendor/**',
        '!.DS_Store/**',
        '!integrations/gutenberg/src/**',
        '!integrations/gutenberg/dist/shortcode.css.map',
        '!integrations/gutenberg/dist/shortcode.js.map',
        '!src/**',
        '!.browserslistrc',
        '!.eslintrc.js',
        '!.prettierrc',
        '!.tm_properties',
        '!README.md',
        '!gulpfile.js',
        '!postcss.config.js',
        '!package.json',
        '!package-lock.json',
        '!composer.json',
        '!composer.lock',
        '!webpack.config.js',
        '!codecanyon.html',
        '!dist/wpstory-premium.css.map',
        '!dist/wpstory-premium.js.map',
        '!dist/wpstory-premium.js.LICENSE.txt',
        '!dist/wpstory-premium-admin.css.map',
        '!dist/wpstory-premium-admin.js.map',
        '!dist/wpstory-premium-submit.css.map',
        '!dist/wpstory-premium-submit.js.map',
        '!dist/wpstory-premium-submit.js.LICENSE.txt',
      ],
      {
        base: '.',
      }
    )
    .pipe(
      gulpRename(function (path) {
        path.dirname = 'rtle-story/' + path.dirname
      })
    )
    .pipe(gulpZip('rtle-story.zip'))
    .pipe(gulp.dest(desktopDir))
}
