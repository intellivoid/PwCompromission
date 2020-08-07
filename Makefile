clean:
	rm -rf build

build:
	mkdir build
	ppm --no-intro --compile="src/pwc" --directory="build"

install:
	ppm --no-prompt --fix-conflict --install="build/net.intellivoid.pwc.ppm"