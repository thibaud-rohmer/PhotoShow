<?php
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * \brief
 *      Interface for something that can be called.
 *
 * This interface provides a generic way to define something that
 * can be invoked to execute some code, like a function, a method
 * (with the usual array representation used by PHP), a closure
 * (when using PHP >= 5.3.0), etc.
 */
interface XRL_CallableInterface
{
    /**
     * Returns the callable object in its raw form
     * (as used by PHP).
     *
     * \retval string
     *      The name of the function this callable represents,
     *      which can be either a core function, a user-defined
     *      function, or the result of a call to create_function().
     *
     * \retval array
     *      An array whose contents matches the definition
     *      of a PHP callback, that is:
     *      -   The first element refers to either an object,
     *          a class name or one of the reserved keywords
     *          (self, parent, static, etc.).
     *      -   The second element is the name of a method
     *          from that object/class.
     *
     * \retval object
     *      Either a Closure object or an instance of a class
     *      that implements the __invoke() magic method.
     *      Both of these are only possible with PHP >= 5.3.0.
     */
    public function getCallable();

    /**
     * Returns a human representation of this callable.
     * For (anonymous) functions, this is a string containing
     * the name of that function.
     * For methods and classes that implement the __invoke()
     * magic method (including Closures), this is a string
     * of the form "ClassName::methodname".
     *
     * \retval string
     *      Human representation of this callable.
     */
    public function getRepresentation();

    /**
     * Implementation of the __invoke() magic method.
     *
     * This method is present only for forward-compatibility
     * and because it turns instances of XRL_CallableInterface
     * into callables themselves (ain't that neat?).
     *
     * \deprecated
     *      Use XRL_CallableInterface::invoke()
     *      instead of calling this method directly
     *      or relying on its magic with code such as:
     *      \code
     *          $c = new XRL_Callable("var_dump");
     *          $c(42);
     *      \endcode
     */
    public function __invoke(/* ... */);

    /**
     * Invokes the callable object represented by this
     * instance.
     *
     * \retval mixed
     *      Value returned by the inner callable.
     *
     * \note
     *      Any argument passed to this method will
     *      be propagated to the inner callable.
     *
     * \note
     *      This method is smart enough to preserve
     *      references.
     */
    public function invoke(/* ... */);

    /**
     * Invokes the callable object represented by this
     * instance, using the given array as a list of arguments.
     *
     * \param array $args
     *      An array whose values will become the arguments
     *      for the inner callable.
     *
     * \retval mixed
     *      Value returned by the inner callable.
     *
     * \note
     *      This method is smart enough to preserve
     *      references.
     */
    public function invokeArgs(array &$args);

    /**
     * Alias for XRL_CallableInterface::getRepresentation().
     *
     * \retval string
     *      Human representation of this callable.
     *
     * \see XRL_CallableInterface::getRepresentation()
     */
    public function __toString();
}
