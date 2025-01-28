# PHP CLI task tracker

> Made for roadmap.sh - Task tracker project, [see more here](https://roadmap.sh/projects/task-tracker)
> Upvote the solution on roadmap https://roadmap.sh/projects/task-tracker/solutions?u=64e0c392ced78d29352ba6dd 
# Downloading the program

If for whatever reason, you want to download this, all you need to do is have [php](https://www.php.net/downloads.php) installed and [git](https://git-scm.com/downloads)

## 1. Clone

Clone the repo with `git clone https://github.com/NotGhoull/php-task-tracker.git` and then cd into the directory `cd php-task-tracker`.

## 2. Run it

To run it just use `php main.php <command here>` running `main.php` on its own will show you the help along with the `help` command.

# Commands

The program supports all the commands as shown in https://roadmap.sh/projects/task-tracker those being:

```bash
# Adding a new task
add <title>

# Updating and deleting tasks
update <id> <title>
delete <id>

# Marking tasks
mark-in-progress <id>
mark-done <id>

# listing
list

# Listing a catagory
list done
list todo
list in-progress
```
