import curses


fullscreen = curses.initscr()

fullscreen.border(0)

fullscreen.addstr(12,25,"Hola mundo desde python curses!")

fullscreen.refresh()

fullscreen.getch()

curses.endwin()
